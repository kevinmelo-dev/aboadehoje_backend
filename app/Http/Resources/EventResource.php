<?php

namespace App\Http\Resources;

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
                'who_liked' => LikeResource::collection($this->likes),
            ],
            'author' => new AuthorResource($this->user),
        ];
    }
}
