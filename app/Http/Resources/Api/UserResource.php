<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // if (is_null($this->resource)) {
        //     return $this;
        // }
        switch ($this->is_lock){
            case -1:
                $this->is_lock = '已删除';
                break;
            case 0:
                $this->is_lock = '正常';
                break;
            case 1:
                $this->is_lock = '冻结';
                break;
        }
        return [
            'id'=>$this->id,
            'username' => $this->username,
            'is_lock' => $this->is_lock,
            'lesson_logs_id' => $this->lesson_logs_id,
            'lesson_title' => $this->lesson_title,
            'post_path' => $this->post_path,
            'created_at'=>(string)$this->created_at,
            'updated_at'=>(string)$this->updated_at
        ];
    }
}
