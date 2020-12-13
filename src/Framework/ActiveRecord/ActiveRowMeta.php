<?php

final class ActiveRowMeta {

    /**
     * @param ActiveRow $obj
     *
     * @return static
     */
    public function getMetaObject(ActiveRow $obj): self {
        /** @var static[] $STATIC_CACHE */
        static $STATIC_CACHE = [];

        // DEBUG
        // var_dump($STATIC_CACHE);

        $cacheKey = \get_class($obj);
        if (!empty($STATIC_CACHE[$cacheKey])) {
            return $STATIC_CACHE[$cacheKey];
        }

        foreach (\get_object_vars($obj) as $PropertyName => $PropertyValue) {
            $this->{$PropertyName} = $PropertyName;
        }

        $STATIC_CACHE[$cacheKey] = $this;

        return $this;
    }

}
