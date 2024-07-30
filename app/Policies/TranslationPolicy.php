<?php

namespace App\Policies;

use App\Models\User;
use TomatoPHP\FilamentTranslations\Models\Translation;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranslationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_translation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Translation $translation): bool
    {
        return $user->can('view_translation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_translation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Translation $translation): bool
    {
        return $user->can('update_translation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Translation $translation): bool
    {
        return $user->can('delete_translation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_translation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Translation $translation): bool
    {
        return $user->can('force_delete_translation');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_translation');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Translation $translation): bool
    {
        return $user->can('restore_translation');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_translation');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Translation $translation): bool
    {
        return $user->can('replicate_translation');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_translation');
    }
}
