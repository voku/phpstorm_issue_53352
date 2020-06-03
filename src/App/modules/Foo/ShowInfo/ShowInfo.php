<?php

class ShowInfo {

    public function renderOk(): void
    {
        $fooCollection = (new FooFactory())->fetchAll();

        $selectArray = [];
        foreach ($fooCollection as $foo) {
            $user_id = $foo->user_id;
            $selectArray[$user_id] = $user_id;
        }

        echo implode(', ', $selectArray);
    }

    public function renderError(): void
    {
        $fooCollection = FooFactory::create()->fetchAll();

        $selectArray = [];
        foreach ($fooCollection as $foo) {
            $user_id = $foo->user_id;
            $selectArray[$user_id] = $user_id;
        }

        echo implode(', ', $selectArray);
    }
}