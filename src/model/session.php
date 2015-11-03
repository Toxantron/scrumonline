<?php
// model/session.php
/**
 * @Entity @Table(name="sessions")
 **/
class Session
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    /** @Column(type="string") **/
    protected $name;
}