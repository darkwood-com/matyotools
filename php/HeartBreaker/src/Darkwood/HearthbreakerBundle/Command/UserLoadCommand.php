<?php

namespace Darkwood\HearthbreakerBundle\Command;

use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class UserLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('users:load')
            ->setDescription('Load users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $userCardService = $container->get('hb.userCard');

        /** @var \Symfony\Component\HttpKernel\Kernel $kernel */
        $kernel = $container->get('kernel');
        $json = file_get_contents($kernel->getRootDir().'/../save.json');

        $decoder = new JsonDecode();
        $data = $decoder->decode($json, 'json', array('json_decode_associative' => true));

        foreach ($data as $username => $cards) {
            $user = $container->get('hb.user')->findOneByUsername($username);

            foreach ($cards as $card) {
                $card['card'] = $container->get('hb.card')->findBySlug($card['card']);

                /** @var \Darkwood\HearthbreakerBundle\Entity\UserCard $userCard */
                $userCard = $userCardService->findOneByUserAndCard($user, $card['card'], $card['isGolden']);
                if (!$userCard) {
                    $userCard = new UserCard();
                    $userCard->setUser($user);
                    $userCard->setCard($card['card']);
                    $userCard->setIsGolden($card['isGolden']);
                    $userCard->setQuantity($card['quantity']);

                    $container->get('hb.usercard')->save($userCard);
                }
            }
        }
    }
}
