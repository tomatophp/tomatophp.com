#!/usr/bin/env bash
# Usage: tools/pr-sweep.sh <repo-name> [--apply]
#
# Reviews open PRs on tomatophp/<repo-name>:
#   - dependabot PRs touching only .github/**: merged when mergeable and CI is not failing,
#     otherwise asked to rebase (the repo's dependabot-auto-merge workflow takes it from there).
#   - every other PR: listed for a human/agent decision, never merged automatically.
# Without --apply it only reports what it would do.
set -uo pipefail

repo=tomatophp/${1:?usage: pr-sweep.sh <repo-name> [--apply]}
apply=${2:-}

# GitHub computes mergeability lazily and reports UNKNOWN until a PR is requested; ask once, wait, then read.
for n in $(gh pr list --repo "$repo" --state open --limit 100 --json number --jq '.[].number'); do
    gh pr view "$n" --repo "$repo" --json mergeable >/dev/null 2>&1
done
sleep 8

prs=$(gh pr list --repo "$repo" --state open --limit 100 --json number,title,author,mergeable,mergeStateStatus,files,isDraft)

echo "$prs" | php -r '
$prs = json_decode(stream_get_contents(STDIN), true) ?: [];
foreach ($prs as $pr) {
    $files = array_column($pr["files"] ?? [], "path");
    $workflowOnly = $files && ! array_filter($files, fn ($f) => ! str_starts_with($f, ".github/"));
    $bot = ($pr["author"]["login"] ?? "") === "app/dependabot";
    $kind = ($bot && $workflowOnly) ? "bump" : "review";
    printf("%s\t%d\t%s\t%s\t%s\n", $kind, $pr["number"], $pr["mergeable"], $pr["mergeStateStatus"], $pr["title"]);
}' | while IFS=$'\t' read -r kind number mergeable state title; do
    case "$kind" in
        bump)
            if [ "$mergeable" = "MERGEABLE" ] && [ "$state" != "BLOCKED" ] && [ "$state" != "DIRTY" ]; then
                if [ "$apply" = "--apply" ]; then
                    if gh pr merge "$number" --repo "$repo" --merge --delete-branch >/dev/null 2>&1; then
                        echo "  merged   #$number  $title"
                    else
                        echo "  FAILED   #$number  $title (merge rejected)"
                    fi
                else
                    echo "  would merge #$number  $title"
                fi
            else
                if [ "$apply" = "--apply" ]; then
                    gh pr comment "$number" --repo "$repo" --body "@dependabot rebase" >/dev/null 2>&1 \
                        && echo "  rebase   #$number  $title ($mergeable/$state)" \
                        || echo "  FAILED   #$number  comment"
                else
                    echo "  would ask rebase #$number  $title ($mergeable/$state)"
                fi
            fi
            ;;
        review)
            echo "  NEEDS REVIEW #$number  $title"
            ;;
    esac
done
