<?php
namespace BusinessCentral\Models;


use BusinessCentral\Entity;

/**
 *
 * Class Attachments
 * Auto-generated on: 2020-05-07 09:06:12
 *
 * @property string $parentId
 * @property-read string $id
 * @property string $fileName
 * @property int $byteSize
 * @property string $content
 * @property-read string $lastModifiedDateTime
 *
 */
class Attachments extends Entity
{
    protected static $schema_type = 'attachments';

    protected $fillable = [
        'parentId',
        'id',
        'fileName',
        'byteSize',
        'content',
        'lastModifiedDateTime',
    ];
}