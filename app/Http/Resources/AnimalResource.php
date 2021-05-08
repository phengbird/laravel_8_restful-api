<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request); //original
        return [
            'id' => $this->id,
            'type_id' => isset($this->type) ? $this->type->id : null,
            'type_name' => isset($this->type) ? $this->type->name : null,
            'name' => $this->name,
            'birthday' => $this->birthday,
            'age' => $this->age,
            'area' => $this->area,
            'fix' => $this->fix,
            'description' => $this->description,
            'personality' => $this->personality,
            'created_at' => (string)$this->created_at,
            'update_at' => (string)$this->updated_at,
            'user_id' => $this->user_id
        ]; 
    }
}
