<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class NotificationFilter extends ApiFilter
{
    protected $safeParms = [
        'receiverId' => ['eq'],
        'senderId' => ['eq'],
        'status' => ['eq'],
        'readByReceiver' => ['eq'],
    ];

    protected $columnMap = [
        'receiverId' => 'receiver_id',
        'senderId' => 'sender_id',
        'readByReceiver' => 'read_by_receiver',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}
