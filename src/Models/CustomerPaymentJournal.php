<?php
namespace BusinessCentral\Models;


use BusinessCentral\Entity;

/**
 *
 * Class CustomerPaymentJournal
 * Auto-generated on: 2020-05-07 09:06:12
 *
 * @property-read string $id
 * @property string $code
 * @property string $displayName
 * @property-read string $lastModifiedDateTime
 * @property string $balancingAccountId
 * @property string $balancingAccountNumber
 * @property-read \BusinessCentral\Models\CustomerPayment[]|\BusinessCentral\EntityCollection $customerPayments
 * @property-read \BusinessCentral\Models\Account $account
 * @method \BusinessCentral\Query\Builder customerPayments()
 * @method \BusinessCentral\Query\Builder account()
 *
 */
class CustomerPaymentJournal extends Entity
{
    protected static $schema_type = 'customerPaymentJournal';

    protected $fillable = [
        'id',
        'code',
        'displayName',
        'lastModifiedDateTime',
        'balancingAccountId',
        'balancingAccountNumber',
    ];
}