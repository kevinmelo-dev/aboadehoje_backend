<?php

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'username' => $this->username,
            'avatar' => asset('uploads/users/' . $this->avatar),
            'events' => Event::query()
                ->where('user_id', $this->id)
                ->orderBy('date', 'DESC')
                ->get()
                ->makeHidden(['user_id', 'created_at', 'updated_at']),
        ];
    }
}
