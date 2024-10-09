<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use TomatoPHP\FilamentNotes\Models\Note;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('view_any_note');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('view_note');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('create_note');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('update_note');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('delete_note');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('delete_any_note');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('force_delete_note');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('force_delete_any_note');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('restore_note');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('restore_any_note');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User|Account $user, Note $note): bool
    {
        return ($user instanceof Account) ? false : $user->can('replicate_note');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User|Account $user): bool
    {
        return ($user instanceof Account) ? false : $user->can('reorder_note');
    }
}
