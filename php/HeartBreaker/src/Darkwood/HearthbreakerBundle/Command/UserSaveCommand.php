<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UserSaveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('users:save')
            ->setDescription('Save users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$container = $this->getContainer();
		$userCardService = $container->get('hb.userCard');

		$data = array();

		$userCards = $userCardService->findAll();
		foreach($userCards as $userCard)
		{
			/** @var \Darkwood\HearthbreakerBundle\Entity\UserCard $userCard */
			$user = $userCard->getUser();
			$card = $userCard->getCard();
			$data[$user->getUsername()][] = array(
				'isGolden' => $userCard->getIsGolden(),
				'quantity' => $userCard->getQuantity(),
				'card' => $card->getSlug()
			);
		}

		$encoder = new JsonEncoder();
		$json = $encoder->encode($data, JsonEncoder::FORMAT);

		/** @var \Symfony\Component\HttpKernel\Kernel $kernel */
		$kernel = $container->get('kernel');
		file_put_contents($kernel->getRootDir().'/../save.json', $json);
    }
}
