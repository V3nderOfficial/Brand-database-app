<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Database\Row;
use Nette\Database\UniqueConstraintViolationException;

/**
 * @author Viktor Miko
 * @since 5.4.2023
 * @package \App\Presenters
 */
class RenameBrandPresenter extends Presenter
{

    /** @var Row|null $brand riadok reprezentujúci značku **/
    private ?Row $brand = null;

    public function __construct(private Connection $connection)
    {
    }

    /**
     * Defaultná akcia
     *
     * @param string $id
     * @return void
     * @throws AbortException
     */
    public function actionDefault(string $id): void
    {
        $this->brand = $this->connection->query('SELECT * FROM brands WHERE id = ?', intval($id))->fetch();

        if ($this->brand === null)
        {
            $this->flashMessage('Značka nebola nájdená', 'error');
            $this->redirect('Homepage:default');
        }
    }

    /**
     * Vytvorí komponent formulár na update značky
     *
     * @return Form
     */
    public function createComponentRenameBrandForm(): Form
    {
        $form = new Form();

        $form->addText('name', 'Názov značky:')
            ->setDefaultValue($this->brand['name'])
            ->setRequired();

        $form->addSubmit('submit', 'Premenovať značku');

        $form->onSuccess[] = function (Form $form, array $values)
        {
            try
            {
                $this->connection->query('UPDATE brands SET name = ? WHERE id = ?', $values['name'], $this->brand['id']);
                $this->flashMessage('Značka bola úspečne premenovaná.', 'success');
            }
            catch (UniqueConstraintViolationException)
            {
                $this->flashMessage('Názov značky už existuje.', 'error');
                $this->redirect('RenameBrand:default', [$this->brand['id']]);
            }

            $this->redirect('Homepage:default');
        };

        return $form;
    }


}