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
  
    public getId()
    {
        return $this->id;
    }
  
    // Getter and setter for topic field
    public getTopic()
    {
        return $this->topic;
    }
    public setTopic($topic)
    {
        $this->topic = $topic;
    }
  
  	 // Getter and setter for result field
    public getResult()
    {
        return $this->result;
    }
    public setResult($result)
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