package webcaisse.modele;

import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

import java.text.ParseException;
import java.text.SimpleDateFormat;

class TestPointsFidelite {

    @Test
    void testAddFideliteTampon() throws ParseException {
        ConsoFidele consoTest = new ConsoFidele("Lifo", "Paul", "lifo.paul@gmail.com", "0600000000",
                new SimpleDateFormat("yyyy-MM-dd").parse("1961-01-03"),
                new SimpleDateFormat("yyyy-MM-dd").parse("2017-01-05"));

        consoTest.addFidelite(1, 50);
        assertEquals(1, consoTest.getPointsFidelite(), "Erreur calcul 1er tampon");

        consoTest.addFidelite(1, 20);
        assertEquals(2, consoTest.getPointsFidelite(), "Erreur calcul 2ème tampon");
    }

    @Test
    void testAddFideliteMontant() throws ParseException {
        ConsoFidele consoTest = new ConsoFidele("Lifo", "Paul", "lifo.paul@gmail.com", "0600000000",
                new SimpleDateFormat("yyyy-MM-dd").parse("1961-01-03"),
                new SimpleDateFormat("yyyy-MM-dd").parse("2017-01-05"));

        consoTest.addFidelite(2, 150);
        assertEquals(150, consoTest.getPointsFidelite(), "Erreur calcul 1er achat");

        consoTest.addFidelite(2, 250);
        assertEquals(400, consoTest.getPointsFidelite(), "Erreur calcul 2ème achat");
    }

    @Test
    void testInitConso() throws ParseException {
        ConsoFidele consoTest = new ConsoFidele("Lifo", "Paul", "lifo.paul@gmail.com", "0600000000",
                new SimpleDateFormat("yyyy-MM-dd").parse("1961-01-03"),
                new SimpleDateFormat("yyyy-MM-dd").parse("2017-01-05"));

        assertEquals(0, consoTest.getPointsFidelite(), "Erreur : Les points de fidélité initiaux doivent être à 0");
    }

    @Test
    void testAddMontant() throws ParseException {
        ConsoFidele consoTest = new ConsoFidele("Lifo", "Paul", "lifo.paul@gmail.com", "0600000000",
                new SimpleDateFormat("yyyy-MM-dd").parse("1961-01-03"),
                new SimpleDateFormat("yyyy-MM-dd").parse("2017-01-05"));

        consoTest.addFidelite(3, 150);
        assertEquals(10, consoTest.getPointsFidelite(), "Erreur tranche 1 (100-200€)");

        consoTest.addFidelite(3, 300);
        assertEquals(30, consoTest.getPointsFidelite(), "Erreur tranche 2 (201-500€)");

        consoTest.addFidelite(3, 600);
        assertEquals(80, consoTest.getPointsFidelite(), "Erreur tranche 3 (>500€)");

        consoTest.addFidelite(3, 50);
        assertEquals(80, consoTest.getPointsFidelite(), "Erreur hors tranche (<100€)");
    }
}
