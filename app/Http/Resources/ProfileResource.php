<?php

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Like;
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
        $query = Like::query()->where('user_id', $this->id)->get();
        $events_i_liked = [];

        for($i = 0; $i < $query->count(); $i++) {
            if(Event::query()->where('id', $query[$i]['event_id'])->first())
                $events_i_liked[$i] = Event::query()
                    ->where('id', $query[$i]['event_id'])
                    ->first()
                    ->makeHidden(['created_at', 'updated_at']);
        }

        return [
            'id' => $this->id,
            'username' => $this->username,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'avatar' => $this->avatar,
            'events' => [
                'my_events' => Event::query()
                    ->where('user_id', $this->id)
                    ->orderBy('date', 'DESC')
                    ->get()
                    ->makeHidden(['user_id', 'created_at', 'updated_at']),
                'liked' => $events_i_liked,
            ],
        ];
    }
}
