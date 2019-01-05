<?php
/*
 * Class that represents an issue loaded from an external source
 * or from a file.
 */
class Issue
{
  // Id of the issue
  public $id;

  // Topic of the issue
  public $topic;

  // Create issue with id and topic
  function _ctor($id, $topic)
  {
    $this->id = $id;
    $this->topic = $topic;
  }
}
