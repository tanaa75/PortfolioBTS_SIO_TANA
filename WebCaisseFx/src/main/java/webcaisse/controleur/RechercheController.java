package webcaisse.controleur;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import webcaisse.dao.GrcDAO;
import webcaisse.modele.Conso;
import java.time.LocalDate;
import java.util.ArrayList;

public class RechercheController {

    @FXML
    private TextField txtSeuil;
    @FXML
    private DatePicker dpDateDebut;
    @FXML
    private DatePicker dpDateFin;
    @FXML
    private Label lblMessage;
    @FXML
    private TableView<Conso> tableConso;
    @FXML
    private TableColumn<Conso, String> colNom;
    @FXML
    private TableColumn<Conso, String> colPrenom;
    @FXML
    private TableColumn<Conso, String> colMail;
    @FXML
    private TableColumn<Conso, String> colTel;
    @FXML
    private TableColumn<Conso, Integer> colNbVentes;

    @FXML
    public void initialize() {
        colNom.setCellValueFactory(new PropertyValueFactory<>("nom"));
        colPrenom.setCellValueFactory(new PropertyValueFactory<>("prenom"));
        colMail.setCellValueFactory(new PropertyValueFactory<>("mail"));
        colTel.setCellValueFactory(new PropertyValueFactory<>("tel"));
        colNbVentes.setCellValueFactory(new PropertyValueFactory<>("nbVentes"));

        dpDateDebut.setValue(LocalDate.now().minusDays(30));
        dpDateFin.setValue(LocalDate.now());
        txtSeuil.setText("1");
    }

    @FXML
    public void handleRechercher() {
        lblMessage.setText("");
        try {
            int seuil = Integer.parseInt(txtSeuil.getText());
            LocalDate dateDeb = dpDateDebut.getValue();
            LocalDate dateFin = dpDateFin.getValue();

            if (dateDeb == null || dateFin == null) {
                lblMessage.setText("Veuillez sélectionner les deux dates.");
                return;
            }

            ArrayList<Conso> resultats = GrcDAO.listeConsoAFideliser(seuil, dateDeb, dateFin);
            ObservableList<Conso> obsList = FXCollections.observableArrayList(resultats);
            tableConso.setItems(obsList);

            if (resultats.isEmpty()) {
                lblMessage.setText("Aucun consommateur trouvé pour ces critères.");
            }
        } catch (NumberFormatException e) {
            lblMessage.setText("Le seuil de ventes doit être un nombre entier.");
        }
    }
}
