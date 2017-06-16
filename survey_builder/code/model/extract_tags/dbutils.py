import ConfigParser

class DBConfig:
    def __init__(self, filename='db.ini', section='mysql'):
        self.filename = filename
        self.section = section

    def read_db_config(self):
        """ Read database configuration file and return a dictionary object
        :param filename: name of the configuration file
        :param section: section of database configuration
        :return: a dictionary of database parameters
        """
        # create parser and read ini configuration file
        parser = ConfigParser.ConfigParser()
        parser.read(self.filename)

        # get section, default to mysql
        db = {}
        if parser.has_section(self.section):
            items = parser.items(self.section)
            for item in items:
                db[item[0]] = item[1]
        else:
            raise Exception('{0} not found in the {1} file'.format(self.section, self.filename))

        return db
