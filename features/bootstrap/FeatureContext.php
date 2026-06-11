<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use FleetParking\App\Command\CreateFleetCommand;
use FleetParking\App\Command\ParkVehicleCommand;
use FleetParking\App\Command\RegisterVehicleCommand;
use FleetParking\App\Handler\CreateFleetHandler;
use FleetParking\App\Handler\ParkVehicleHandler;
use FleetParking\App\Handler\RegisterVehicleHandler;
use FleetParking\Domain\Exception\VehicleAlreadyParkedAtLocation;
use FleetParking\Domain\Exception\VehicleAlreadyRegistered;
use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\FleetRepository;
use FleetParking\Domain\Fleet\UserId;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;
use FleetParking\Infra\Postgres\PdoFactory;
use FleetParking\Infra\Postgres\PostgresFleetRepository;
use FleetParking\Infra\Postgres\PostgresTestHelper;
use PHPUnit\Framework\Assert;

final class FeatureContext implements Context
{
    private bool $useInfra = false;
    private ?FleetRepository $repository = null;
    private Fleet $myFleet;
    private Fleet $otherFleet;
    private string $myFleetId = '';
    private string $otherFleetId = '';
    private VehiclePlateNumber $vehicle;
    private Location $location;
    private ?Throwable $lastException = null;

    /** @BeforeScenario */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->lastException = null;
        $this->useInfra = in_array('infra', $scope->getScenario()->getTags(), true);

        if (!$this->useInfra) {
            return;
        }

        $pdo = PdoFactory::fromEnvironment();
        PostgresTestHelper::reset($pdo);
        $this->repository = new PostgresFleetRepository($pdo);
    }

    /** @Given my fleet */
    public function myFleet(): void
    {
        if ($this->useInfra) {
            $this->myFleetId = (new CreateFleetHandler($this->repository))(new CreateFleetCommand('user-1'));

            return;
        }

        $this->myFleet = Fleet::create(new UserId('user-1'));
    }

    /** @Given the fleet of another user */
    public function theFleetOfAnotherUser(): void
    {
        if ($this->useInfra) {
            $this->otherFleetId = (new CreateFleetHandler($this->repository))(new CreateFleetCommand('user-2'));

            return;
        }

        $this->otherFleet = Fleet::create(new UserId('user-2'));
    }

    /** @Given a vehicle */
    public function aVehicle(): void
    {
        $this->vehicle = new VehiclePlateNumber('AB-123-CD');
    }

    /** @Given a location */
    public function aLocation(): void
    {
        $this->location = new Location(48.8566, 2.3522);
    }

    /** @Given I have registered this vehicle into my fleet */
    public function iHaveRegisteredThisVehicleIntoMyFleet(): void
    {
        if ($this->useInfra) {
            (new RegisterVehicleHandler($this->repository))(
                new RegisterVehicleCommand($this->myFleetId, (string) $this->vehicle),
            );

            return;
        }

        $this->myFleet->registerVehicle($this->vehicle);
    }

    /** @Given this vehicle has been registered into the other user's fleet */
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet(): void
    {
        if ($this->useInfra) {
            (new RegisterVehicleHandler($this->repository))(
                new RegisterVehicleCommand($this->otherFleetId, (string) $this->vehicle),
            );

            return;
        }

        $this->otherFleet->registerVehicle($this->vehicle);
    }

    /** @Given my vehicle has been parked into this location */
    public function myVehicleHasBeenParkedIntoThisLocation(): void
    {
        if ($this->useInfra) {
            (new ParkVehicleHandler($this->repository))(
                new ParkVehicleCommand(
                    $this->myFleetId,
                    (string) $this->vehicle,
                    $this->location->latitude,
                    $this->location->longitude,
                    $this->location->altitude,
                ),
            );

            return;
        }

        $this->myFleet->parkVehicle($this->vehicle, $this->location);
    }

    /** @When I register this vehicle into my fleet */
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        if ($this->useInfra) {
            (new RegisterVehicleHandler($this->repository))(
                new RegisterVehicleCommand($this->myFleetId, (string) $this->vehicle),
            );

            return;
        }

        $this->myFleet->registerVehicle($this->vehicle);
    }

    /** @When I try to register this vehicle into my fleet */
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        try {
            $this->iRegisterThisVehicleIntoMyFleet();
        } catch (Throwable $exception) {
            $this->lastException = $exception;
        }
    }

    /** @When I park my vehicle at this location */
    public function iParkMyVehicleAtThisLocation(): void
    {
        if ($this->useInfra) {
            (new ParkVehicleHandler($this->repository))(
                new ParkVehicleCommand(
                    $this->myFleetId,
                    (string) $this->vehicle,
                    $this->location->latitude,
                    $this->location->longitude,
                    $this->location->altitude,
                ),
            );

            return;
        }

        $this->myFleet->parkVehicle($this->vehicle, $this->location);
    }

    /** @When I try to park my vehicle at this location */
    public function iTryToParkMyVehicleAtThisLocation(): void
    {
        try {
            $this->iParkMyVehicleAtThisLocation();
        } catch (Throwable $exception) {
            $this->lastException = $exception;
        }
    }

    /** @Then this vehicle should be part of my vehicle fleet */
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        Assert::assertTrue($this->loadedMyFleet()->hasVehicle($this->vehicle));
    }

    /** @Then I should be informed this this vehicle has already been registered into my fleet */
    public function iShouldBeInformedThisThisVehicleHasAlreadyBeenRegisteredIntoMyFleet(): void
    {
        Assert::assertInstanceOf(VehicleAlreadyRegistered::class, $this->lastException);
    }

    /** @Then the known location of my vehicle should verify this location */
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation(): void
    {
        Assert::assertTrue($this->location->equals($this->loadedMyFleet()->knownLocationOf($this->vehicle)));
    }

    /** @Then I should be informed that my vehicle is already parked at this location */
    public function iShouldBeInformedThatMyVehicleIsAlreadyParkedAtThisLocation(): void
    {
        Assert::assertInstanceOf(VehicleAlreadyParkedAtLocation::class, $this->lastException);
    }

    private function loadedMyFleet(): Fleet
    {
        if ($this->useInfra) {
            return $this->repository->get(new FleetId($this->myFleetId));
        }

        return $this->myFleet;
    }
}
