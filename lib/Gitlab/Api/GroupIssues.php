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
}
