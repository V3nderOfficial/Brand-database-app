<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Database\UniqueConstraintViolationException;

/**
 * @author Viktor Miko
 * @since 4.4.2023
 * @package \App\Presenters
 */
class AddNewBrandPresenter extends Presenter
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * Vytvorí komponent formulár na pridanie novej značky
     *
     * @return Form
     */
    public function createComponentAddNewBrandForm(): Form
    {
        $form = new Form();

        $form->addText('name', 'Názov značky:')
            ->setRequired('Prosím, zadajte názov značky.');

        $form->addSubmit('submit', 'Pridať značku');

        $form->onSuccess[] = function (Form $form, array $values): void
        {
            try
            {
                $this->connection->query('INSERT INTO brands (name, created_by) VALUES (?, ?)', $values['name'], 1);
                $this->flashMessage('Značka bola úspečne pridaná.', 'success');
            }
            catch (UniqueConstraintViolationException)
            {
                $this->flashMessage('Názov značky už existuje.', 'error');
                $this->redirect('AddNewBrand:default');
            }

            $this->redirect('Homepage:default');
        };

        return $form;
    }
}