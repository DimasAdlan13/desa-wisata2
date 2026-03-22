<?php

namespace App\Services;

use App\Models\Service;
use App\Models\User;

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

        return $service->fresh();
    }
}
