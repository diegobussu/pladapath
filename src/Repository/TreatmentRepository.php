<?php

namespace App\Repository;

use App\Entity\Treatment;
use App\Entity\Vm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Treatment>
 *
 * @method Treatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Treatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Treatment[]    findAll()
 * @method Treatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Treatment::class);
    }

    public function add(Treatment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Treatment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTraitementsForDate(\DateTimeInterface $selectedDate)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.date_debut <= :selectedDate')
            ->andWhere('t.date_fin >= :selectedDate')
            ->setParameter('selectedDate', $selectedDate)
            ->getQuery()
            ->getResult();
    }
    
    public function find($id, $lockMode = null, $lockVersion = null): ?object
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTraitementForVmAndDateRange(Vm $vm, \DateTime $dateDebut, \DateTime $dateFin)
    {
        return $this->createQueryBuilder('t')
            ->join('t.id_vm', 'v') 
            ->where(':vm MEMBER OF t.id_vm')
            ->andWhere('t.date_debut <= :dateFin')
            ->andWhere('t.date_fin >= :dateDebut')
            ->andWhere('t.etat = :etatEnCours')
            ->setParameter('vm', $vm)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('etatEnCours', 'En cours')
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findLastTraitementForVms(array $vms)
    {
        return $this->createQueryBuilder('t')
            ->join('t.id_vm', 'v')
            ->andWhere('v IN (:vms)')
            ->setParameter('vms', $vms)
            ->orderBy('t.date_fin', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    

//    /**
//     * @return Treatment[] Returns an array of Treatment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Treatment
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
