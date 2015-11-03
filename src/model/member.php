<?php
// model/member.php
/**
 * @Entity @Table(name="members")
 **/
class Member
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    /** @Column(type="string") **/
    protected $name;
	 /** @Column(type="double") **/
    protected $vote;
}