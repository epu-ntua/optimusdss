from distutils.core import setup

requirements = ['setuptools',
                'tweepy',
                'simplejson',
                'facepy',
                'ztreamy',
                'python-dateutil',
                ]

setup(
    name='DataCapturingModules',
    version='0.0.1',
    packages=['Optimus', 'Optimus.Enpro', 'Optimus.Common', 'Optimus.Social'],
    url='http://www.optimus-smartcity.eu',
    author='abot',
    author_email='abot@epu.ntua.gr',
    description='Optimus data capturing modules',
    install_requires = requirements
)
