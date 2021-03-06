package caricaDB;

import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.text.ParseException;



public class Inizio {
	public static void main(String[] args) throws SQLException, ClassNotFoundException, ParseException, IOException{
		//per connettermi al db principale
		String stringaConn = null;
		Connection conn = null;

		//per caricare il db
		Caricatore carica = null;
		

		stringaConn = "jdbc:sqlite:carte.db";
		
		//connessione a mydb
		Class.forName("org.sqlite.JDBC");
		conn = DriverManager.getConnection(stringaConn);	
		
		carica = new Caricatore(conn);
		carica.carica("colori", "colori.csv");
		carica.carica("espansioni", "espansioni.csv");
		carica.carica("rarita", "rarita.csv");
		carica.carica("tipi", "tipi.csv");

		
		
		try {
			conn.close();
			System.out.println("finito");
		} catch (SQLException e) {
			System.out.println("Error closing connection");
		}
	}

}

