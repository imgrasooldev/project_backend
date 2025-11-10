<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function all($perPage = 10)
    {
        return Notification::with(['sender', 'receiver', 'application'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function find($id)
    {
        return Notification::with(['sender', 'receiver', 'application'])->findOrFail($id);
    }

    public function filter(array $filters, $perPage = 10)
    {
        $query = Notification::with(['sender', 'receiver', 'application']);

        foreach ($filters as $filter) {
            $query->where($filter[0], $filter[1], $filter[2]);
        }

        $query->orderBy('id', 'desc');
        return $query->paginate($perPage);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update([
            'read_by_receiver' => true,
            'read_by_receiver_at' => now(),
        ]);
        return $notification;
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('receiver_id', $userId)
            ->update([
                'read_by_receiver' => true,
                'read_by_receiver_at' => now(),
            ]);
    }

    /**
     * Get total unread notifications for a specific user and role.
     */
    public function getUnreadCount(int $userId, string $role = 'receiver')
    {
        $column = $role === 'sender' ? 'sender_id' : 'receiver_id';
        $readFlag = $role === 'sender' ? 'read_by_sender' : 'read_by_receiver';

        return $this->model
            ->where($column, $userId)
            ->where($readFlag, false)
            ->count();
    }
}
