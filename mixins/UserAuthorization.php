<?php

namespace Mixins;

use Exception;
use CustomExceptions\AuthorizationException;

use Illuminate\Database\Eloquent\Model;

/**
 * Design to only used with User model.
 */
trait UserAuthorization
{
    /**
     * Is authorized admin logic dependent on `is_admin` property in `User` model
     * @return bool
     */
    public function isAuthorizedAdmin(): bool
    {
        return (bool)$this->is_admin;
    }

    /**
     * Model should have `user_id` property,
     * User will be authorized if user_id of model match the id of user
     * @param Model $model
     * @param bool $skip_admin
     * @return bool
     * @throws Exception if model hasn't `user_id` column.
     */
    public function isAuthorizedTo($model, $skip_admin = true): bool
    {
        /**
         * bad use of helper method.
         */
        if ($model->user_id === null)
        {

            throw new Exception("to validate if user authorized to, the model should has user_id property.");
        }

        /**
         * is user authorized scenarios:
         * #authorized means return true
         * #not-authorized means return false
         *
         * if he is the model owner & he is an admin -> #authorized
         * if he is the model owner but isn't an admin -> #authorized
         * if he isn't the model owner and isn't an admin -> #not-authorized
         * if he isn't the model owner, but he is an admin -> #authorized
         * if he isn't the model owner, but he is an admin & skip_admin flag is false -> #not-authorized
         */
        $is_authorized_admin = $this->isAuthorizedAdmin();
        if ($this->id != $model->user_id && ! ($skip_admin && $is_authorized_admin)) {

            return false;
        }

        return true;

    }

    /**
     * @throws AuthorizationException if user isn't authorized admin.
     */
    public function validateIsAuthorizedAdmin(): void
    {
        $is_authorized_admin = $this->isAuthorizedAdmin();
        if (! $is_authorized_admin) {

            throw new AuthorizationException("this API isn't allowed for none admin users.");
        }

    }

    /**
     * Is authorized to Update/Delete/Read arbitrary model (model must have `user_id` property)
     * @throws AuthorizationException if user isn't authorized
     */
    public function validateIsAuthorizedTo($model, $skip_admin = true): void
    {
        $is_authorized = $this->isAuthorizedTo($model, $skip_admin);
        if (! $is_authorized) {

            throw new AuthorizationException("not allowed.");
        }

    }

}