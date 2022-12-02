<?php

namespace Chaos\ResponseBuilder\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MessageResource
 *
 * @package Chaos\ResponseBuilder\Resources
 * @property string tr
 * @property string en
 * @property string ru
 * @property string uk
 * @property string es
 * @property string de
 * @property string he
 * @property string ar
 * @property string pt
 * @property string ja
 */
class MessageResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'tr' => $this["tr"],
            'en' => $this["en"],
            'ru' => $this["ru"],
            'uk' => $this["uk"],
            'es' => $this["es"],
            'de' => $this["de"],
            'he' => $this["he"],
            'ar' => $this["ar"],
            'pt' => $this["pt"],
            'ja' => $this["ja"]
        ];
    }
}
