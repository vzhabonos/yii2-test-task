<?php

namespace tests\functional;

use FunctionalTester;

class SiteCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/index');
    }

    public function checkIndexPage(FunctionalTester $I)
    {
        $I->see('Import from XLS To MongoDB', 'h5');
        $I->see('Synchronize MongoDB to Opensearch', 'h5');
        $I->see('Aggregation from Opensearch', 'h5');
        $I->seeElement('form');
    }

    public function testSyncAction(FunctionalTester $I)
    {
        $I->submitForm('#sync-mongo-to-opensearch-form', []);
        $I->see('Sync completed', '.alert-success');
    }
}
