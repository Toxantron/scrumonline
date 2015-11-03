<?php
// model/session.php
/**
 * @Entity @Table(name="votes")
 **/
class Vote
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
  
    /** @Column(type="float") **/
    protected $value;
  
    /** @ManyToOne(targetEntity="Poll", inversedBy="votes") **/
    protected $poll;
  
    /** @ManyToOne(targetEntity="Member", inversedBy="votes") **/
    protected $member;
  
    public getId()
    {
        return $this->id;
    }
  
    // Getter and setter for value field
    public getValue()
    {
        return $this->value;
    }
    public setValue($value)
    {
        $this->value = $value;
    }
  
    // Getter and setter for poll association
    public getPoll()
    {
        return $this->poll;
    }
    public setPoll($poll)
    {
        $this->poll = $poll;
    }
  
    // Getter and setter for value field
    public getMember()
    {
        return $this->member;
    }
    public setMember($member)
    {
        $this->member = $member;
    }
}