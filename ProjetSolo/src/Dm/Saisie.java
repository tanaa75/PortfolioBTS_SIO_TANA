package Dm ;
import java.io.*;
import java.util.*;
class Saisie {
	 
	public static String lire_String(String question)

	{
	String ligne_lue=null;
	try

	{
	InputStreamReader isr= new InputStreamReader(System.in);


	BufferedReader br=new BufferedReader(isr);

	System.out.println (question);

	ligne_lue=br.readLine();
	}
	catch(IOException e)
	{
	System.out.println(e);
	}
	return ligne_lue;
	}
	public static int lire_int(String question)

	{
	return Integer.parseInt(lire_String(question));
	}
	public static double lire_double(String question)
	{
	return Double.parseDouble(lire_String(question));
	
	}
}