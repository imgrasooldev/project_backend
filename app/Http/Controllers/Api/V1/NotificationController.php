<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\FirebaseService;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Filters\V1\NotificationFilter;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\V1\NotificationCollection;
use App\Http\Resources\V1\NotificationResource;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationController extends BaseController
{
    protected $notificationRepo;

    public function __construct(NotificationRepositoryInterface $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * GET /api/v1/notifications
     */
    /* public function index(Request $request)
    {
        $filter = new NotificationFilter();
        $filterItems = $filter->transform($request);
        $perPage = $request->input('per_page', 10);

        $notifications = $this->notificationRepo->filter($filterItems, $perPage);

        $success = new NotificationCollection($notifications->appends($request->query()));
        return $this->sendResponse($success, 'Notifications retrieved successfully.');
    } */
  public function index(Request $request)
{
    $filter = new NotificationFilter();
    $filterItems = $filter->transform($request);
    $perPage = $request->input('per_page', 10);

    // Notifications (paginated)
    $notifications = $this->notificationRepo->filter($filterItems, $perPage);

    // Unread count (independent of pagination)
    $user = $request->user();
    $role = $request->input('role', 'receiver');
    $unreadCount = $this->notificationRepo->getUnreadCount($user->id, $role);

    $response = [
        'unread_count' => $unreadCount, // ✅ not tied to pagination
        'notifications' => new NotificationCollection(
            $notifications->appends($request->query())
        ),
    ];

    return $this->sendResponse($response, 'Notifications retrieved successfully.');
}




    /**
     * GET /api/v1/notifications/{id}
     */
    public function show($id)
    {
        $notification = $this->notificationRepo->find($id);
        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification fetched successfully.'
        );
    }

    /**
     * POST /api/v1/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        $notification = $this->notificationRepo->markAsRead($id);
        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification marked as read.'
        );
    }

    /**
     * POST /api/v1/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $this->notificationRepo->markAllAsRead($user->id);

        return $this->sendResponse([], 'All notifications marked as read.');
    }

    public function sendTest()
    {
        $firebase = new FirebaseService();

        $deviceToken = "ca7zBGDgRWa1785Ku593R9:APA91bGQDInBqOn_6b40bfE878PQ7WirkUpb1_3jLqsKYgXGvdxie26V82MzsPsDiHglXp97187RHcEzm54c86qYllK3oj1RBFxGTmDwutIj8qzx4F9hdFQ";

        $result = $firebase->sendNotification(
            $deviceToken,
            "Hello 🚀",
            "This is a test notification",
            ["order_id" => "123 sparta"]
        );

        return response()->json($result);
    }
}