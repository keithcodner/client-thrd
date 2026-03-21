<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'from_id',
        'fk_circle_item_post_id',
        'fk_conversation_id',
        'fk_rankings_id',
        'fk_ranking_transaction_history_id',
        'fk_pronetwork_requests_id',
        'fk_comments_id',
        'fk_trxn_payment_transaction_id',
        'fk_circle_transaction_id',
        'fk_verify_images_id',
        'notif_an_id',
        'type',
        'title',
        'comment',
        'note_table_name_target',
        'note_table_related_id',
        'note_relation_description',
        'op_1',
        'op_2',
        'op_3',
        'status',
        'color_status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get random color from predefined set
     * 
     * @return string
     */
    public static function getRandomColor(): string
    {
        $colors = [
            '#8B7355', // sage brown
            '#6B7280', // stone gray
            '#92400E', // clay brown
            '#D97706', // amber
            '#7C2D12', // dusk brown
            '#B45309', // sand orange
            '#059669', // emerald
            '#0891B2', // cyan
            '#4F46E5', // indigo
            '#7C3AED', // violet
        ];
        
        return $colors[array_rand($colors)];
    }
}
