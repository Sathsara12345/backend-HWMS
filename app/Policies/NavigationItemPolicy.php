<?php

namespace App\Policies;

use App\Models\NavigationItem;
use App\Models\User;

class NavigationItemPolicy
{
    /**
     * The authenticated user must belong to the same hotel.
     * Adjust this logic to match your User ↔ Hotel relationship.
     */
    private function ownsHotel(User $user, int $hotelId): bool
    {
        if (!$hotelId) return false;

        // Super admin can access everything
        if ($user->hasRole('super-admin')) return true;

        // Check if the user's hotel matches the requested hotel
        return (int) $user->hotel?->id === (int) $hotelId;
    }

    public function viewAny(User $user, int $hotelId): bool
    {
        return $this->ownsHotel($user, $hotelId);
    }

    public function view(User $user, NavigationItem $item): bool
    {
        return $this->ownsHotel($user, $item->hotel_id);
    }

    public function create(User $user, int $hotelId): bool
    {
        return $this->ownsHotel($user, $hotelId);
    }

    public function update(User $user, NavigationItem $item): bool
    {
        return $this->ownsHotel($user, $item->hotel_id);
    }

    public function delete(User $user, NavigationItem $item): bool
    {
        return $this->ownsHotel($user, $item->hotel_id);
    }
}
