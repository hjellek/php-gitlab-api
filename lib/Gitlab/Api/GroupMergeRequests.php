<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class GroupMergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';
    const STATE_MERGED = 'merged';
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';

    /**
     * @param int   $group_id
     * @param array $parameters {
     *
     *     @var int[]              $iids           Return the request having the given iid.
     *     @var string             $state          Return all merge requests or just those that are opened, closed, or
     *                                             merged.
     *     @var string             $order_by       Return requests ordered by created_at or updated_at fields. Default
     *                                             is created_at.
     *     @var string             $sort           Return requests sorted in asc or desc order. Default is desc.
     *     @var string             $milestone      Return merge requests for a specific milestone.
     *     @var string             $view           If simple, returns the iid, URL, title, description, and basic state
     *                                             of merge request.
     *     @var string             $labels         Return merge requests matching a comma separated list of labels.
     *     @var \DateTimeInterface $created_after  Return merge requests created after the given time (inclusive).
     *     @var \DateTimeInterface $created_before Return merge requests created before the given time (inclusive).
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined.
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules.
     *
     * @return mixed
     */
    public function all($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('c');
        };
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'opened', 'merged', 'closed'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('milestone');
        $resolver->setDefined('view')
            ->setAllowedValues('view', ['simple'])
        ;
        $resolver->setDefined('labels');
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created_by_me', 'assigned_to_me', 'all'])
        ;
        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer');

        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer');

        $resolver->setDefined('search');
        $resolver->setDefined('source_branch');
        $resolver->setDefined('target_branch');

        return $this->get($this->getGroupPath($group_id, 'merge_requests'), $resolver->resolve($parameters));
    }
}
