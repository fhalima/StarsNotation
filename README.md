#Notation en étoiles
Ce projet consiste à réaliser un système de notation en étoile avec JQuery et CSS. J'ai choisi de réaliser ce travail avec Symfony 5.
Les étapes à suivre scrupuleusement pour avoir l'effet escompé pour les notation en étoiles.

####Création du projet Symfony

Executer la commande :
```
composer create-project symfony/skeleton my_project_name
```

##Installer les dépendances 


* Installer [NodeJs](#https://nodejs.org/fr/download/)

* Installer et lancer le serveur web de Symfony (sous Windows)
```
> composer require Symfony/web-server-bundle --dev^4.4.2
> php bin/console server:run
```
- Installer les annotations (indispensables pour le routage)
```
composer require annotations
```
* Installer le maker 
```
composer require maker --dev
```
* Installer le Twig
```
composer require twig
```
* Installer [yarn](#https://classic.yarnpkg.com/en/docs/install/#windows-stable)
* Installer le Webpack Encore :
```
> composer require symfony/webpack-encore-bundle
> yarn install
> yarn add @symfony/webpack-encore --dev
> yarn add webpack-notifier@^1.6.0 --dev
> yarn add sass-loader@^11.0.0 node-sass --dev
> yarn add core-js
> yarn add jquery
> yarn add @fortawesome/fontawesome-free
```
* Lancer le Weboack encore 
```
 yarn encore dev --watch
```
####Création du contrôleur
- Executer la commande suivante
```
php bin/console make:controller
```
- Puis désigner le nom de votre choix pour ce controleur (Ex : HomeController).
- Ajouter le code suivant au controlleur:
```
class HomeController extends AbstractController
{
    /**
 * @Route("/", name="home")
 */
    public function index(UrlGeneratorInterface $router, Request $request,
                          EntityManagerInterface $em,
                          NoteRepository $noteRepository)
    {
        $noteList = $noteRepository->findAll();
        $note = new Note();
        $noteForm = $this->createForm(NoteFormType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            $note = $noteForm->getData();
            $note->getValue($request->request->get("value"));

            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Note enregistrée');
            return $this->redirectToRoute('home');
        }


        return $this->render('Notation.html.twig', [
            'note_form' => $noteForm->createView(),
            'note_list'=>$noteList]);
    }

    /**
     * @Route("/note-delete/{id}", name="delete_note")
     */
    public function deleteNote(Request $request,
                          EntityManagerInterface $em,
                          NoteRepository $noteRepository)
    {

        $id = $request->get('id');
        $note_curr = $noteRepository->findOneBy(["id" => $id]);
        $em->remove($note_curr);
        $em->flush();
        $this->addFlash('success', 'votre note/commentaire a bien était supprimée.');
        return $this->redirectToRoute('home');
    }
}

```
Les classes Note et noteRepository seront créées par la suite.

####Création et migration de la base de donnée

- Installation de Doctrine
```
composer require orm
```
- Creation de la base de données
```
php bin/console doctrine:database:create
```
- Mise à jour du fichier .env (du projet) : définir les parameètres d'accès à la base de données nouvellement créée.
```
DATABASE_URL="mysql://Nom_Utilisateur_BD:Mot_de_Passe_BD@localhost:3306/Nom_Base_de_données"
``` 
- Création de l'entité Note
```
php bin/console make:entity
```
Suivre les étapes pour la création de la classe Note.php avec les propriétés : Value (Integer), comment(Text), dateRegister(DateTime)
- Effectuer la migration 
```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
####Code JQuery
Insérer le code JQuery dans le fichier app.js
```
import './styles/app.scss';

require('jquery');

//--------------------------------Gestion de la notation en étoiles -------------------------------//
let firstconnect = 1;
if (firstconnect === 1) {
    SetStartsGold($("#note_form_value").val());
    SetStartsGray(parseInt($("#note_form_value").val()) + 1);
    firstconnect = 0;
}
for (let i = 1; i <= 5; i++) {
    $("#star" + i).click(function () {
        $("#note_form_value").val(i);
        SetStartsGold(i);
        SetStartsGray(i + 1);
    });

    let note_value = $("#note_form_value").val();

    $("#star" + i).hover(function () {
            SetStartsGold(i);
            SetStartsGray(i + 1);
        }, function () {
            SetStartsGold(note_value);
            SetStartsGray(parseInt(note_value) + 1);
        }
    );
}
//fonction de coloriage des etoiles de notation
function SetStartsGold(i) {
    $("#star" + i).css('color', "#ffdc0f");
    if (i > 0)
        SetStartsGold(i - 1);

}

function SetStartsGray(i) {
    $("#star" + i).css('color', "#808080");
    if (i < 5)
        SetStartsGray(i + 1);

}

```
####Code CSS
Insérer le code suivant dans le fichier app.scss
```
@import "~@fortawesome/fontawesome-free/css/all.min.css";

body {
    font-family: Helvetica, Arial, sans-serif;
    background-repeat: repeat;
    background-blend-mode: screen;
    background-color: White;
    margin: 0 !important;

}
.container {
    margin: 0 auto !important;
    width: 90% !important;
    text-align: center;
    min-height: 100vh !important;
    padding: 20%;
}

.stars .fa-star {
    color: gray;
    cursor: pointer;
    font-size: 1em;
}

.stars {
    display: inline-flex;
    font-size: 2em;
}

.is-padding-20{
    padding: 20px !important;
}
.isgold{
    color: gold !important;
}
.isgray{
    color: gray !important;
}
```
####Création du formulaire de NoteFormType
- Executer la commande suivante 
```
php bin/console make:form
```
- Puis suivre les étapes pour la création du formulaire; Le formulaire doit contenir le code suivant:
```
class NoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Les commentaires doivent contenir au moins 10 caractères.',
                        'max' => 500,
                        'maxMessage' => 'Votre commentaire ne peut dépasser les 500 caractères.'
                    ])
                ]
            ])
            ->add('value', HiddenType::class, [
            'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}

```

####Création des la templates

- Dans le repertoire templates, créer le fichier base.html.twig. Ce fichier contient les déclarations des packages JQuery, Bootstrap et Bilma.  
```
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
              integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.4/css/bulma.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma-carousel@4.0.4/dist/css/bulma-carousel.min.css">

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
                integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
                integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
                crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
                integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
                crossorigin="anonymous"></script>

        <title>{% block title %}Notation par etoiles{% endblock %}</title>
         {% block stylesheets %}
            {{ encore_entry_link_tags('app') }}
        {% endblock %}

        {% block javascripts %}
            {{ encore_entry_script_tags('app') }}
        {% endblock %}
    </head>
    <body>
        {% block body '' %}
    </body>
</html>

```
- Créer le fichier notation.html.twig qui hérite de base.html.twig. Dans ce fichier on créera la vue.
```
{% extends "base.html.twig" %}
{% block title %}
    Note
{% endblock %}
{% block body %}
    <div class="container">

        <div class="section has-text-left has-background-white-bis">

            <h3 class="subtitle has-text-primary has-text-weight-bold">
                Notes:
            </h3>

            <div class="section">
                <div class="full-width has-text-centered is-padding-20">
                    <div class="stars" id="stars">
                        <i class="fa fa-star" id="star1"></i>
                        <i class="fa fa-star" id="star2"></i>
                        <i class="fa fa-star" id="star3"></i>
                        <i class="fa fa-star" id="star4"></i>
                        <i class="fa fa-star" id="star5"></i>
                    </div>

                </div>

                {{ form_start(note_form) }}
                {{ form_row(note_form.comment, { label: 'Commentaire (facultatif)' }) }}

                <button class="button is-success" type="submit">
                    Noter
                </button>

                {{ form_end(note_form) }}

            </div>

            {% for note in note_list %}
                {# @var note \App\Entity\Note #}
                <br>
                <div class="card">
                    <div class="card-content">
                        <strong>
                            Note:
                            <span class="has-text-primary">{{ note.value }}/5</span>
                            <div class="full-width has-text-centered is-padding-20">
                                <div>
                                    <i class="fa fa-star {{ (note.value >=1) ? ' isgold' : ' isgray' }}" id="star1"></i>
                                    <i class="fa fa-star {{ note.value >=2 ?' isgold' : ' isgray' }}" id="star2"></i>
                                    <i class="fa fa-star {{ note.value >=3 ?' isgold' : ' isgray' }}" id="star3"></i>
                                    <i class="fa fa-star {{ note.value >=4 ?' isgold' : ' isgray' }}" id="star4"></i>
                                    <i class="fa fa-star {{ note.value >=5 ?' isgold' : ' isgray' }}" id="star5"></i>
                                </div>
                            </div>
                        </strong>
                        <br>
                        {{ note.comment }}
                        <hr>
                        <small>
                            <a href="{{ path('delete_note', {id: note.id}) }}" class="button is-danger is-small">
                                <i class="far fa-times-circle"></i>&nbsp;
                                Supprimer
                            </a>

                            le <b>{{ note.dateregister|date('d/m/Y') }}</b>
                            à <b>{{ note.dateregister|date('H:i') }}</b>.
                        </small>
                    </div>
                </div>
            {% endfor %}

        </div>
    </div>>
{% endblock %}
```
- On peut visualiser la vue sur le lien [localhost](#http://localhost:8000/)