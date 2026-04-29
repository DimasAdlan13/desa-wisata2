<?php

namespace App\Services;

use App\Models\Service;
use App\Models\User;
use App\Notifications\AdminApprovedNotification;
use App\Notifications\ServiceStatusNotification;

class ApprovalService
{
    /**
     * Approve an admin_layanan user account.
     */
    public function approveUser(User $user, User $approvedBy): User
    {
        $user->update([
            'is_approved' => true,
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);

        $user->notify(new AdminApprovedNotification(approved: true));

        return $user->fresh();
    }

    /**
     * Revoke an admin_layanan user approval.
     */
    public function revokeUser(User $user): User
    {
        $user->update([
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $user->notify(new AdminApprovedNotification(approved: false));

        return $user->fresh();
    }

    /**
     * Approve a service listing.
     */
    public function approveService(Service $service, User $approvedBy): Service
    {
        $service->update([
            'is_approved' => true,
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);

        // Notify the service owner
        $service->user?->notify(new ServiceStatusNotification($service->fresh(), approved: true));

        return $service->fresh();
    }

    /**
     * Reject/revoke approval of a service.
     */
    public function revokeService(Service $service): Service
    {
        $service->update([
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Notify the service owner
        $service->user?->notify(new ServiceStatusNotification($service->fresh(), approved: false));

        return $service->fresh();
    }
}
