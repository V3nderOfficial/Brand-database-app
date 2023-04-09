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
        $intCount = $this->getCount();
        $sort = $this->getSort();
        $intPage = $this->getPage();

        $brandsCount = $this->getBrandsCount();
        $maxPage = ceil($brandsCount / $intCount);

        if($intPage > $maxPage)
        {
            $intPage = intval($maxPage);
        }

        $brandNames = $this->getBrandNames($intCount, $sort, $intPage);

        $this->template->setParameters([
            'count' => $intCount,
            'sort' => $sort,
            'page' => $intPage,
            'brandNames' => $brandNames,
            'minPage' => 1,
            'maxPage' => $maxPage
        ]);
    }

    /**
     * Akcia ktorá vymaže značku z table brands
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
     * Metóda premení premennú na integer, zadá jej defaultnú hodnotu a zabráni nežiaduce hodnoty
     *
     * @return int
     */
    private function getCount(): int
    {
        $count = $this->getParameter('count');

        if (is_numeric($count))
        {
            $intCount = intval($count);
        }
        else
        {
            return 10;
        }

        if ($intCount !== 10 && $intCount !== 20 && $intCount !== 30)
        {
            return 10;
        }

        return $intCount;
    }

    /**
     * Metóda zadá defaultnú hodnotu a zabráni nežiaduce hodnoty
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
     * Metóda premení premennú na integer, zadá jej defaultnú hodnotu a zabráni nežiaduce hodnoty
     *
     * @return int
     */
    private function getPage(): int
    {
        $page = $this->getParameter('page');

        if (is_numeric($page))
        {
            $intPage = intval($page);
        }
        else
        {
            return 1;
        }

        if ($intPage <= 0)
        {
            return 1;
        }

        return $intPage;
    }


    /**
     * Metóda vyberie ID a name z table brands
     *
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
     * Metóda pripíše hodnotu k $brandId
     *
     * @return int
     */
    private function getBrandId(): int
    {
        $brandId = $this->getParameter('id');

        return $brandId;
    }
}
