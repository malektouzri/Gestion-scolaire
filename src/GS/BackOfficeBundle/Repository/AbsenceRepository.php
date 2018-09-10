<?php

namespace GS\BackOfficeBundle\Repository;

/**
 * AbsenceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AbsenceRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAbs($classe,$mat,$date){
        $query = $this->getEntityManager()->createQuery('select a from BackOfficeBundle:Absence a 
where a.classe = (select c from BackOfficeBundle:Classe c where c.id = :classe)
 AND a.idMat = (select m from BackOfficeBundle:Matiere m where m.id = :mat) AND a.date = :date')
            ->setParameters(array('classe'=>$classe,'mat'=>$mat,'date'=>$date));
        return $query->getResult();
    }

    public function getAbse($classe,$mat){
        $query = $this->getEntityManager()->createQuery('select a from BackOfficeBundle:Absence a 
where a.classe = (select c from BackOfficeBundle:Classe c where c.id = :classe)
 AND a.idMat = (select m from BackOfficeBundle:Matiere m where m.id = :mat) ')
            ->setParameters(array('classe'=>$classe,'mat'=>$mat));
        return $query->getResult();
    }
}
