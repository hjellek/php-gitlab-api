<?php namespace Gitlab\Api;

class GroupIssues extends AbstractApi
{
    /**
     * @param int $group_id
     * @param array $parameters (
     *
     *     @var string $state     Return all issues or just those that are opened or closed.
     *     @var string $labels    Comma-separated list of label names, issues must have all labels to be returned.
     *                            No+Label lists all issues with no labels.
     *     @var string $milestone The milestone title.
     *     @var string scope      Return issues for the given scope: created-by-me, assigned-to-me or all. Defaults to created-by-me
     *     @var int[]  $iids      Return only the issues having the given iid.
     *     @var string $order_by  Return requests ordered by created_at or updated_at fields. Default is created_at.
     *     @var string $sort      Return requests sorted in asc or desc order. Default is desc.
     *     @var string $search    Search issues against their title and description.
     * )
     *
     * @return mixed
     */
    public function all($group_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['opened', 'closed'])
        ;
        $resolver->setDefined('labels');
        $resolver->setDefined('milestone');
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created-by-me', 'assigned-to-me', 'all'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('search');

        $path = $group_id === null ? 'issues' : $this->getGroupPath($group_id, 'issues');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @return mixed
     */
    public function show($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)));
    }

    /**
     * @param int $group_id
     * @param array $params
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post($this->getGroupPath($group_id, 'issues'), $params);
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param array $params
     * @return mixed
     */
    public function update($group_id, $issue_iid, array $params)
    {
        return $this->put($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)), $params);
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param int $to_group_id
     * @return mixed
     */
    public function move($group_id, $issue_iid, $to_group_id)
    {
        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/move', array(
            'to_group_id' => $to_group_id
        ));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @return mixed
     */
    public function remove($group_id, $issue_iid)
    {
        return $this->delete($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @return mixed
     */
    public function showComments($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/notes');
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param int $note_id
     * @return mixed
     */
    public function showComment($group_id, $issue_iid, $note_id)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/notes/'.$this->encodePath($note_id));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string|array $body
     * @return mixed
     */
    public function addComment($group_id, $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/notes'), $params);
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param int $note_id
     * @param string $body
     * @return mixed
     */
    public function updateComment($group_id, $issue_iid, $note_id, $body)
    {
        return $this->put($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)), array(
            'body' => $body
        ));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param int $note_id
     * @return mixed
     */
    public function removeComment($group_id, $issue_iid, $note_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @return mixed
     */
    public function showDiscussions($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions');
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @return mixed
     */
    public function showDiscussion($group_id, $issue_iid, $discussion_id)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions/'.$this->encodePath($discussion_id));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string|array $body
     * @return mixed
     */
    public function addDiscussion($group_id, $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/discussions'), $params);
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param string|array $body
     * @return mixed
     */
    public function addDiscussionNote($group_id, $issue_iid, $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes'), $params);
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param int $note_id
     * @param string $body
     * @return mixed
     */
    public function updateDiscussionNote($group_id, $issue_iid, $discussion_id, $note_id, $body)
    {
        return $this->put($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)), array(
            'body' => $body
        ));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param int $note_id
     * @return mixed
     */
    public function removeDiscussionNote($group_id, $issue_iid, $discussion_id, $note_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $duration
     */
    public function setTimeEstimate($group_id, $issue_iid, $duration)
    {
        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/time_estimate'), array('duration' => $duration));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     */
    public function resetTimeEstimate($group_id, $issue_iid)
    {
        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/reset_time_estimate'));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @param string $duration
     */
    public function addSpentTime($group_id, $issue_iid, $duration)
    {
        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/add_spent_time'), array('duration' => $duration));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     */
    public function resetSpentTime($group_id, $issue_iid)
    {
        return $this->post($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/reset_spent_time'));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     * @return mixed
     */
    public function getTimeStats($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid) .'/time_stats'));
    }

    /**
     * @param int $group_id
     * @param int $issue_iid
     *
     * @return mixed
     */
    public function awardEmoji($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid).'/award_emoji'));
    }

    /**
    * @param int $group_id
    * @param int $issue_iid
    * @return mixed
    */
    public function closedByMergeRequests($group_id, $issue_iid)
    {
        return $this->get($this->getGroupPath($group_id, 'issues/'.$this->encodePath($issue_iid)).'/closed_by');
    }
}
