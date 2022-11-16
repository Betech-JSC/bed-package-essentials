<?php

namespace JamstackVietnam\Core\Traits;

use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\File;

trait HasRichText
{
    public static function bootHasRichText()
    {
        static::saving(function ($model) {
            if (request()->route() === null) return;

            $model->getAllRichTextColumns();
        });
    }

    private function getAllRichTextColumns()
    {
        $columns = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableColumns($this->getTable());

        foreach ($columns as $column) {
            if ($column->getType()->getName() === 'text') {
                $this->storeExternalMedia($column->getName());
            };
        }
    }

    private function storeExternalMedia($field)
    {
        $regex = '/src\s*=\s*"(.+?)"/m';

        $content = $this->{$field};

        if (empty($content)) {
            return;
        }

        preg_match_all($regex, $content, $matches, PREG_SET_ORDER, 0);

        if (empty($matches)) {
            return;
        }

        foreach ($matches as $match) {
            $url = $match[1];

            if (strstr($url, 'static/')) continue;

            try {
                $newUrl = (new File)->storeFromUrl($url);

                if ($newUrl) {
                    $content = str_replace($url, $newUrl, $content);
                    $this->{$field} = $content;
                }
            } catch (\Exception $exception) {
                logger()->error('Can not store image: ' . $url);
                logger()->error($th->getMessage());
            }
        }
    }
}
