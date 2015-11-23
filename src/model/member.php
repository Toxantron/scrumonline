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
  
    /** @ManyToOne(targetEntity="Session", inversedBy="members") **/
    protected $session;
  
    /** @OneToMany(targetEntity="Vote", mappedBy="member", orphanRemoval=true) **/
    protected $votes;
  
    public function getId()
    {
    	  return $this->id;
    }
  
    // Getter and setter for name field
    public function getName()
    {
    	  return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
  
    // Getter and setter for session field
    public function getSession()
    {
    	  return $this->session;
    }
    public function setSession($session)
    {
        $this->session = $session;
        $session->getMembers()->add($this);
    }
  
    // Getter for votes association
    public function getVotes()
    {
    	  return $this->votes;
    }
}
