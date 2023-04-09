<?php declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\AbortException;
use Nette\Database\Connection;

/**
 * @author Viktor Miko
 * @since 16.3.2023
 * @package \App\Presenters
 */
class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /**
     * Constructor
     *
     * @param Connection $connection
     */
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @return void
     */
    public function renderDefault(): void
    {
        $count = $this->getCount();
        $sort = $this->getSort();
        $page = $this->getPage();

        $brandsCount = $this->getBrandsCount();
        $maxPage = ceil($brandsCount / $count);

        if($page > $maxPage)
        {
            $page = intval($maxPage);
        }

        $brandNames = $this->getBrandNames($count, $sort, $page);

        $this->template->setParameters([
            'count' => $count,
            'sort' => $sort,
            'page' => $page,
            'brandNames' => $brandNames,
            'minPage' => 1,
            'maxPage' => $maxPage
        ]);
    }

    /**
     * Akcia ktorá vymaže značku
     *
     * @return void
     * @throws AbortException
     */
    public function actionDeleteBrand(): void
    {
        $brandId = $this->getBrandId();

        if($brandId === null)
        {
            $this->flashMessage('Nepodarilo sa vymazať značku, ID značky je povinná.', 'error');
        }
        else
        {
            $this->connection->query("DELETE FROM brands WHERE id = ?", $brandId);

            $this->flashMessage('Značka úspešne vymazaná.', 'success');
        }

        $this->redirect('Homepage:default');
    }

    /**
     * Načíta count z URL
     *
     * @return int
     */
    private function getCount(): int
    {
        $count = $this->getParameter('count');

        if (is_numeric($count))
        {
            $count = intval($count);
        }
        else
        {
            return 10;
        }

        if ($count !== 10 && $count !== 20 && $count !== 30)
        {
            return 10;
        }

        return $count;
    }

    /**
     * Načíta sort z URL
     *
     * @return string
     */
    private function getSort(): string
    {
        $sort = $this->getParameter('sort');

        if ($sort !== 'asc' && $sort !== 'desc')
        {
            return 'asc';
        }

        return $sort;
    }

    /**
     * Načíta page z URL
     *
     * @return int
     */
    private function getPage(): int
    {
        $page = $this->getParameter('page');

        if (is_numeric($page))
        {
            $page = intval($page);
        }
        else
        {
            return 1;
        }

        if ($page <= 0)
        {
            return 1;
        }

        return $page;
    }


    /**
     * Metóda vyberie ID a name z table brands
     *
     * @param int $count
     * @param string $sort
     * @param int $page
     * @return array
     */
    private function getBrandNames(int $count, string $sort, int $page): array
    {
        $offset = $count * ($page - 1);

        return $this->connection->query("SELECT id, name FROM brands ORDER BY name $sort LIMIT $count OFFSET $offset")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getBrandsCount(): int
    {
        return $this->connection->query("SELECT COUNT(*) AS `count` FROM brands")->fetch()?->count ?? 0;
    }

    /**
     * Metóda načíta brand ID z URL adresy
     *
     * @return int
     */
    private function getBrandId(): int
    {
        return $this->getParameter('id');
    }
}
