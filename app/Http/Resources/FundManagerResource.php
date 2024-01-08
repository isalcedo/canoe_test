<?php

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\FundManager;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FundManager
 */
class FundManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            //Relationships
            'funds' => FundResource::collection($this->whenLoaded('funds')),
        ];
    }
}
