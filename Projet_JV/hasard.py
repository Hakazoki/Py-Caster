import random

class degat:
    def run(self):
        return(random.randint(1, 20))

class hp:
    def run(self):
        return(random.randint(10, 25))

if __name__ == "__main__":
    import sys
    class_name = sys.argv[1]
    classes = {
        "hp": hp,
        "degat": degat
    }
    if class_name in classes:
        instance = classes[class_name]()
        print(instance.run())
    else:
        print("Classe inconnue")
