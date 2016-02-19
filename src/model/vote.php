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
  
    /** @Column(type="boolean") **/
    protected $highlighted = false;
  
    /** @ManyToOne(targetEntity="Poll", inversedBy="votes") **/
    protected $poll;
  
    /** @ManyToOne(targetEntity="Member", inversedBy="votes") **/
    protected $member;
  
    public function getId()
    {
        return $this->id;
    }
  
    // Getter and setter for value field
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }

    // Getter and setter for start time
    public function getHighlighted()
    {
        return $this->highlighted; 
    }
    public function setHighlighted($highlighted)
    {
        $this->highlighted = $highlighted;
    }
  
    // Getter and setter for poll association
    public function getPoll()
    {
        return $this->poll;
    }
    public function setPoll($poll)
    {
        $this->poll = $poll;
        $poll->getVotes()->add($this);
    }
  
    // Getter and setter for value field
    public function getMember()
    {
        return $this->member;
    }
    public function setMember($member)
    {
        $this->member = $member;
        $member->getVotes()->add($this);
    }
}