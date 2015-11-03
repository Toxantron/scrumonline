<?php
// model/session.php
/**
 * @Entity @Table(name="polls")
 **/
class Poll
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
  
    /** @Column(type="string") **/
    protected $topic;
  
    /** @Column(type="float") **/
    protected $result;
  
    /** @ManyToOne(targetEntity="Session", inversedBy="polls") **/
    protected $session;
  
    /** @OneToMany(targetEntity="Vote", mappedBy="poll") **/
    protected $votes;
  
    public function getId()
    {
        return $this->id;
    }
  
    // Getter and setter for topic field
    public function getTopic()
    {
        return $this->topic;
    }
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }
  
  	 // Getter and setter for result field
    public function getResult()
    {
        return $this->result;
    }
    public function setResult($result)
    {
        $this->result = $result;
    }
  
    // Getter and setter for session field
    public function getSession()
    {
    	  return $this->session;
    }
    public function setSession($session)
    {
        $this->session = $session;
    }
  
    // Getter and setter for votes association
    public function getVotes()
    {
    	  return $this->votes;
    }
}