<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    public function all($perPage);
    public function find($id);
    public function filter(array $filters, $perPage);
    public function markAsRead($id);
    public function markAllAsRead($userId);
    /**
     * Count unread notifications for sender or receiver.
     *
     * @param int $userId
     * @param string $role  'receiver' | 'sender'
     * @return int
     */
    public function getUnreadCount(int $userId, string $role = 'receiver');
}
