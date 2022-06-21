<?php

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * @var mixed
     */
    /**
     * @var mixed
     */

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
            'image' => $this->image,
            'title' => $this->title,
            'about' => $this->about,
            'local' => $this->local,
            'date' => $this->date,
            'time' => $this->time,
            'price' => $this->price,
            'category' => $this->category,
            'likes' => [
                'count' => LikeResource::collection($this->likes)->count(),
                'users' => LikeResource::collection($this->likes),
            ],
            'author' => AuthorResource::collection(User::query()->where('id', $this->user_id)->get()),
        ];
    }
}
