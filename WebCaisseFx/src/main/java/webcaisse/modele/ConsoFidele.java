package webcaisse.modele;

import java.util.Date;

public class ConsoFidele extends Conso {
    private Date dateNaiss;
    private double pointsFidelite;
    private Date dateInscription;

    public ConsoFidele(String nom, String prenom, String mail, String tel, Date dateNaiss, Date dateInscription) {
        super(nom, prenom, mail, tel, 0);
        this.dateNaiss = dateNaiss;
        this.dateInscription = dateInscription;
        this.pointsFidelite = 0;
    }

    public double getPointsFidelite() {
        return this.pointsFidelite;
    }

    public void addFidelite(int typeFidelite, double montant) {
        switch (typeFidelite) {
            case 1:
                this.pointsFidelite++;
                break;
            case 2:
                this.pointsFidelite += montant;
                break;
            case 3:
                if (montant >= 100 && montant <= 200) {
                    this.pointsFidelite += 10;
                } else if (montant > 200 && montant <= 500) {
                    this.pointsFidelite += 20;
                } else if (montant > 500) {
                    this.pointsFidelite += 50;
                }
                break;
        }
    }
}
