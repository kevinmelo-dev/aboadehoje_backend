<?php

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->username,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'avatar' => $this->avatar,
            'events' => Event::query()
                ->where('user_id', $this->id)
                ->orderBy('date', 'DESC')
                ->get()
                ->makeHidden(['user_id', 'created_at', 'updated_at']),
        ];
    }
}
