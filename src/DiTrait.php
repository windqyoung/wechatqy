<?php


namespace Wqy\WechatQy;


trait DiTrait
{
    /**
     * @var \Pimple\Container
     */
    private $di;

    /**
     * @return the $di
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @param field_type $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

}
