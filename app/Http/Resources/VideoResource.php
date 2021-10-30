<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'year_launched' => $this->year_launched,
            'opened' => $this->opened,
            'rating' => $this->rating,
            'duration' => $this->duration,
            'video_file' => $this->video_file,
            'thumb_file' => $this->thumb_file,
            'banner_file' => $this->banner_file,
            'trailer_file' => $this->trailer_file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'categories_id' => $this->categories_id,
            'genres_id' => $this->genres_id,
            //'categories_id' => $this->whenLoaded('categories_id'),
            //'genres_id' => $this->whenLoaded('genres_id'),
        ];
    }
}
