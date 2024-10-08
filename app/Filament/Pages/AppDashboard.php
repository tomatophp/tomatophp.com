<?php

namespace App\Filament\Pages;

use App\Jobs\PreloadIssuesForRepos;
use App\Services\RepoService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use TomatoPHP\FilamentCms\Jobs\GitHubMetaRefreshJob;
use TomatoPHP\FilamentSocial\Filament\Actions\SocialShareAction;

class AppDashboard extends Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateOpenSource')
                ->requiresConfirmation()
                ->color('info')
                ->icon('bxl-github')
                ->label('Sync GitHub Docs')
                ->action(function (){
                    dispatch(new GitHubMetaRefreshJob());

                    Notification::make()
                        ->title('GitHub Docs Synced')
                        ->body('The GitHub docs have been synced successfully.')
                        ->success()
                        ->send();

                    return redirect()->back();
                }),
            Action::make('updateIssues')
                ->requiresConfirmation()
                ->color('warning')
                ->icon('bx-git-repo-forked')
                ->label("Sync GitHub Issues")
                ->action(function (){
                    $batches = app(RepoService::class)
                        ->reposToCrawl()
                        ->chunk(25)
                        ->map(function (Collection $repos): PreloadIssuesForRepos {
                            return new PreloadIssuesForRepos($repos);
                        })
                        ->all();

                    Bus::batch($batches)
                        ->then(function (): void {
                            Artisan::call('issues:tweet');
                        })
                        ->dispatch();

                    Notification::make()
                        ->title('GitHub Issues Synced')
                        ->body('The GitHub issues have been synced successfully.')
                        ->success()
                        ->send();

                    return redirect()->back();
                }),
        ];
    }
}
