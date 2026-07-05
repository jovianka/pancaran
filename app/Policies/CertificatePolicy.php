<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * The certificate owner, or anyone who manages the event's certificates,
     * may view it.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        return $certificate->user_id === $user->id
            || $user->hasEventPermission($certificate->event, 'download_certificates');
    }

    public function download(User $user, Certificate $certificate): bool
    {
        return $this->view($user, $certificate);
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->hasEventPermission($certificate->event, 'download_certificates');
    }
}
