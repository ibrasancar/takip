<?php $pager->setSurroundCount(10) ?>

<nav aria-label="Page navigation" class="">
    <ul class="pagination">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a aria-label="<?= lang('Pager.first') ?>" class="page-link" href="<?= $pager->getFirst() ?>">
                    <span aria-hidden="true">«</span>
                    <span class="sr-only"><?= lang('Pager.first') ?></span>
                </a>
            </li>
            <li class="page-item">
                <a aria-label="<?= lang('Pager.first') ?>" class="page-link" href="<?= $pager->getPrevious() ?>">
                    <span aria-hidden="true">‹</span>
                    <span class="sr-only"><?= lang('Pager.first') ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()) : ?>
        <li class="page-item">
            <a aria-label="<?= lang('Pager.next') ?>" class="page-link" href="<?= $pager->getNext() ?>">
                <span aria-hidden="true">›</span>
                <span class="sr-only"><?= lang('Pager.next') ?></span>
            </a>
        </li>
        <li class="page-item">
            <a aria-label="<?= lang('Pager.last') ?>" class="page-link" href="<?= $pager->getLast() ?>">
                <span aria-hidden="true">»</span>
                <span class="sr-only"><?= lang('Pager.last') ?></span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>